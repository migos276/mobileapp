<?php
$title = 'Gestion des Stations - GazExpress';
$includeMap = false;

ob_start();
?>

<header class="header">
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-fire"></i>
            <span>GazExpress</span>
        </div>
        <div class="nav-links">
            <span>Admin: <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="/logout" class="btn-login">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </nav>
</header>

<div class="dashboard">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-cog"></i> Administration
        </div>
        <ul class="sidebar-menu">
            <li><a href="/admin/dashboard"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="/admin/stations" class="active"><i class="fas fa-gas-pump"></i> Stations</a></li>
            <li><a href="/admin/orders"><i class="fas fa-list"></i> Commandes</a></li>
            <li><a href="/admin/analytics"><i class="fas fa-chart-bar"></i> Analyses</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <h1>Gestion des Stations</h1>
            <p class="breadcrumb">Admin / Stations</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1rem; padding: 1rem; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 0.25rem;">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger" style="margin-bottom: 1rem; padding: 1rem; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 0.25rem;">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Stations en attente d'approbation -->
        <div class="table-container" style="margin-bottom: 2rem;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3><i class="fas fa-clock"></i> Stations en attente d'approbation</h3>
            </div>
            <div style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($pendingStations)): ?>
                    <div style="padding: 2rem; text-align: center; color: #666;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>Aucune station en attente</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom de l'entreprise</th>
                                <th>Propriétaire</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingStations as $station): ?>
                                <tr>
                                    <td><?= htmlspecialchars($station['nom_entreprise']) ?></td>
                                    <td><?= htmlspecialchars($station['nom']) ?></td>
                                    <td><?= htmlspecialchars($station['email']) ?></td>
                                    <td><?= htmlspecialchars($station['telephone'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($station['adresse'] ?? '') ?></td>
                                    <td>
                                        <form method="POST" action="/admin/stations/approve/<?= $station['id'] ?>" style="display: inline;">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Approuver cette station ?')">
                                                <i class="fas fa-check"></i> Approuver
                                            </button>
                                        </form>
                                        <form method="POST" action="/admin/stations/approve/<?= $station['id'] ?>" style="display: inline; margin-left: 0.5rem;">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Rejeter cette station ?')">
                                                <i class="fas fa-times"></i> Rejeter
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stations approuvées -->
        <div class="table-container">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                <h3><i class="fas fa-gas-pump"></i> Stations approuvées</h3>
            </div>
            <div style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($approvedStations)): ?>
                    <div style="padding: 2rem; text-align: center; color: #666;">
                        <i class="fas fa-gas-pump" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>Aucune station approuvée</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom de l'entreprise</th>
                                <th>Propriétaire</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($approvedStations as $station): ?>
                                <tr>
                                    <td><?= htmlspecialchars($station['nom_entreprise']) ?></td>
                                    <td><?= htmlspecialchars($station['nom']) ?></td>
                                    <td><?= htmlspecialchars($station['email']) ?></td>
                                    <td><?= htmlspecialchars($station['telephone'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($station['adresse'] ?? '') ?></td>
                                    <td>
                                        <span class="badge badge-success">Approuvée</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
include '../app/Views/layouts/app.php';
?>
